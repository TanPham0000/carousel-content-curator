
import React, { useState } from "react";
import ContentForm from "@/components/ContentForm";
import ContentCarousel from "@/components/ContentCarousel";
import ContentManager from "@/components/ContentManager";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Button } from "@/components/ui/button";
import { useContent } from "@/context/ContentContext";
import { ContentProvider } from "@/context/ContentContext";

const Index = () => {
  const [activeTab, setActiveTab] = useState<string>("submit");
  
  return (
    <ContentProvider>
      <div className="container px-4 py-8 mx-auto max-w-6xl">
        <header className="mb-8 text-center">
          <h1 className="text-3xl font-bold tracking-tight mb-2">Carousel Content Curator</h1>
          <p className="text-muted-foreground">Upload and manage content for your carousel display</p>
        </header>
        
        <Tabs defaultValue="submit" value={activeTab} onValueChange={setActiveTab}>
          <div className="flex justify-center mb-6">
            <TabsList>
              <TabsTrigger value="submit">Submit Content</TabsTrigger>
              <TabsTrigger value="manage">Manage Content</TabsTrigger>
            </TabsList>
          </div>
          
          <TabsContent value="submit" className="space-y-8">
            <ContentFormWithPreview />
          </TabsContent>
          
          <TabsContent value="manage">
            <ContentManager />
          </TabsContent>
        </Tabs>
      </div>
    </ContentProvider>
  );
};

const ContentFormWithPreview: React.FC = () => {
  const { contents } = useContent();
  
  return (
    <>
      <ContentForm />
      
      <div className="mt-12">
        <h2 className="text-2xl font-bold text-center mb-6">Preview Carousel</h2>
        <ContentCarousel items={contents} />
      </div>
      
      <div className="mt-8 text-center">
        <p className="text-sm text-muted-foreground mb-4">
          This carousel preview shows how your content will appear on the website.
          All approved submissions will be displayed in the main carousel.
        </p>
      </div>
    </>
  );
};

export default Index;
